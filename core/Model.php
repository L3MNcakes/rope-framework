<?php
if(!defined('ROPE_BASE_PATH')) die('You shall not pass!');
/**
 * @package     RopeFramework
 * @filename    Model.php
 * @author      Brandon Soucie <x3n0ph0be@tormail.org>
 *
 * Class definition for Rope-Framework Model.
 *
 */
class Rope_Model {
    // White-list of accepted field types
    protected $_acceptedFieldTypes = array(
        0 => "string",
        1 => "number",
        2 => "boolean",
        3 => "timestamp",
    );

    /**
     * Protected variables that define the model
     *
     * RiakClient _client -- Client from Riak PHP Library
     * Array _validTags -- Array of valid links to other objects
     * String _bucket -- Name of bucket
     * String _key -- Key for object
     * Array _links -- Array that stores linked objects
     * String _rawData -- JSON string containing the raw data of the Riak Object
     */
    protected $_client;
    protected $_validTags;
    protected $_bucket;
    protected $_key;
    protected $_links = array();
    protected $_rawData;

    /**
     * Public variables the define the model
     *
     * Array _fields -- An array of fields and their corresponding data
     */
    public $_fields = array();

    /**
     * Constructor sets up the RiakClient
     */
    function __construct() {
        $this->_client = new RiakClient('127.0.0.1', '8091');
    }

    /**
     * Method to set the key of the current object
     */
    protected function _setKey($value) {
        $this->_key = $value;
        $this->_fields["uid"]["data"] = $value;
    }

    /**
     * Method to set the rawData attribute on current object
     */
    protected function _setRawData($data) {
        $this->_rawData = $data;
    }

    /**
     * Loads data from a RiakObject returned from the Riak PHP Library
     */
    public function loadFromRiakObj($riakObj) {
        if(!is_object($riakObj)) $riakObj = (object) $riakObj;

        $data = $riakObj->getData();
        foreach($data as $key=>$value) {
            if(array_key_exists($key, $this->_fields)) {
                $this->set($key, $value);
            }
        }
        $this->_setRawData($riakObj);

        $this->_populateLinks();
    }

    /**
     * Stores the data of the current object into the defined bucket
     */
    public function store() {
        if($this->_bucket != "") {
            $bucket = $this->_client->bucket($this->_bucket);

            if($this->_key == "") {
                $this->_setKey(uniqid());
                $this->set("created", time());
            }

            $data = array();
            foreach($this->_fields as $field) {
                if(!in_array($field["type"], $this->_acceptedFieldTypes)) {
                    return False;
                } elseif($field["name"] == "uid") {
                    $data["uid"] = $this->_key; 
                } else {
                    switch($field["type"]) {
                        case "string":
                            $data[$field["name"]] = (string) $field["data"];
                            break;
                        case "number":
                            if(is_int($field["data"]))
                                $data[$field["name"]] = (int) $field["data"];
                            elseif(is_float($field["data"]))
                                $data[$field["name"]] = (float) $field["data"];
                            else
                                $data[$field["name"]] = (string) $field["data"];
                            break;
                        case "boolean":
                            $data[$field["name"]] = (bool) $field["data"];
                            break;
                        case "timestamp":
                            if($field["renew"]) {
                                $data[$field["name"]] = (string) time();
                                $this->set($field["name"], $data[$field["name"]]);
                            }
                            else
                                $data[$field["name"]] = $field["data"];
                            break;
                        default:
                            $data[$field["name"]] = (string) $field["data"];
                    }
                }
            }

            if($riakObj = $bucket->get($this->_key)) {
                $riakObj->setData($data);
                $riakObj->setContentType("text/json");
                $riakObj->store();
                $this->loadFromRiakObj($riakObj);
            } else {
                $riakObj = $bucket->newObject($this->_key, $data);
                $riakObj->setContentType("text/json");
                $riakObj->store();
                $this->loadFromRiakObj($riakObj);
            }

            return True;
        }
        return False;
    }

    /**
     * Deletes the object from the Riak Database
     */
    public function delete() {
        if($this->_key == "")
            return False;

        $bucket = $this->_client->bucket($this->_bucket);
        $riakObj = $bucket->get($this->_key);
        $riakObj->delete();

        return True;
    }

    /**
     * Method to set a field on the current object
     */
    public function set($fieldname, $value) {
        if(!array_key_exists($fieldname, $this->_fields)) {
            return false;
        }

        $fieldType = $this->_fields[$fieldname]["type"];

        switch($fieldType) {
            case "string":
                $this->_fields[$fieldname]["data"] = (string) $value;
                break;
            case "number":
                if(is_int($value))
                    $this->_fields[$fieldname]["data"] = (int) $value;
                elseif (is_float($value))
                    $this->_fields[$fieldname]["data"] = (float) $value;
                else
                    $this->_fields[$fieldname]["data"] = 0;
                break;
            case "boolean":
                $this->_fields[$fieldname]["data"] = (bool) $value;
                break;
            case "timestamp":
                if($this->_fields[$fieldname]["renew"])
                    $this->_fields[$fieldname]["data"] = (string) time();
                else
                    $this->_fields[$fieldname]["data"] = (string) $value;
                break;
            default:
                $this->_fields[$fieldname]["data"] = (string) $value;
        }
    }

    /**
     * Returns the data of a corresponding field
     */
    public function get($fieldname) {
        if(!array_key_exists($fieldname, $this->_fields))
            return "";

        return $this->_fields[$fieldname]["data"];
    }

    /**
     * Looks up an object from the Riak database
     */
    public function find($key) {
        $bucket = $this->_client->bucket($this->_bucket);

        $riakObj = $bucket->get($key);
        $data = $riakObj->getData();
        $this->_setRawData(json_encode($data));

        if(!$data) return False;

        foreach($data as $key=>$value) {
            if(!array_key_exists($key, $this->_fields)) continue;

            $this->set($key, $value);
        }

        $this->_key = $riakObj->getKey();

        return $this;
    }

    /**
     * Returns an array of all the current linked data to this object
     */
    public function getAllLinks() {
        if($this->_links == "") {
            $this->_populateLinks();
        }

        return $this->_links;
    }

    /**
     * Populates links by querying the RIAK database
     */
    private function _populateLinks() {
        if($this->_key == "")
            return False;

        $bucket = $this->_client->bucket($this->_bucket);
        $riakObj = $bucket->get($this->_key);
        $links = $riakObj->getLinks();
        foreach($links as $link) {
            if(array_key_exists($link->getTag(), $this->_links)) {
                $this->_links[$link->getTag()]["matches"][$link->getKey()] = $link->get()->getData();
            } else {
                $this->_links[$link->getTag()] = array(
                    "name" => $link->getTag(),
                    "bucket" => $link->getBucket(),
                    "matches" => array(),
                );

                $this->_links[$link->getTag()]["matches"][$link->getKey()] = $link->get()->getData();
            }
        }
    }

    /**
     * Returns a specific linked object corresponding to a tagname
     */
    public function getLink($tagName) {
        if(!in_array($tagName, $this->_validTags))
            return False;

        if(!array_key_exists($tagName, $this->_links))
            $this->_populateLinks();

        if(isset($this->_links[$tagName]["matches"])) {
            return $this->_links[$tagName]["matches"];
        } else {
            return array();
        }
    }

    /**
     * Adds a linked object with corresponding tag name
     */
    public function addLink($tagName, $lBucket, $lKey) {
        if($this->_key == "" || !in_array($tagName, $this->_validTags) || $lBucket == "" || $lKey == "")
            return False;

        $bucket = $this->_client->bucket($this->_bucket);
        $riakObj = $bucket->get($this->_key);
        $linkBucket = $this->_client->bucket($lBucket);
        $linkObj = $linkBucket->get($lKey);
        $riakObj->addLink($linkObj, $tagName);
        $riakObj->store();
        $this->_populateLinks();

        return True;
    }

    /**
     * Returns the name of the bucket for the current object
     */
    public function getBucket() {
        return $this->_getBucket();
    }
    protected function _getBucket() {
        return $this->_bucket;
    }

    /**
     * Runs a query that returns all Riak Objects in defined bucket
     */
    public function mapGetAll($sort=false) {
        $jsMap = "function(v) { return [v.key]; }";

        $result = $this->_client->add($this->getBucket())
            ->map($jsMap);

        if($sort) {
            $result = $result->reduce("Riak.reduceSort");
        }

        $result = $result->run();

        return $result;
    }
}
