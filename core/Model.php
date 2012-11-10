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
class Rope_Model 
{
    protected $_client;
    protected $_domain;
    protected $_key;
    protected $_keyAttribute;
    protected $_attributes;
    protected $_links;
    protected $_removedLinks;

    public function __construct($key=null,$host=ROPE_RIAK_HOST,$port=ROPE_RIAK_PORT) {
        $this->_client = new RiakClient($host,$port);

        if($key) {
            $this->_key = $key;
            $this->load();
        }
    }

    public function store() {
        if(!isset($this->_attributes) || empty($this->_attributes)) return False;

        if(isset($this->_attributes[$this->_keyAttribute])) {
            $this->_key = $this->_attributes[$this->_keyAttribute];
        }

        $bucket = $this->_client->bucket($this->_domain);
        $obj = new RiakObject($this->_client, $bucket, $this->_key);
        $data = json_encode($this->_attributes);
        $obj->setContentType('application/json');
        $obj->setData($data);

        foreach($this->_links as $tag=>$link) {
            foreach($link as $l) {
                $bucket = $this->_client->bucket($l["bucket"]);
                $linkObj = $bucket->get($l["key"]); 

                $obj->addLink($linkObj, $tag);
            }
        }

        foreach($this->_removedLinks as $k=>$link) {
            $bucket = $this->_client->bucket($link["bucket"]);
            $linkObj = $bucket->get($link["key"]);

            $obj->removeLink($linkObj, $link["tag"]);
            unset($this->_removedLinks[$k]);
        }

        $obj->store();

        $this->_key = $obj->getKey();

        return True;
    }

    public function delete() {
        if(!isset($this->_key) || $this->_key == "") return False;

        $bucket = $this->_client->bucket($this->_domain);
        $obj = $bucket->get($this->_key);
        $obj->delete();
        $this->_key = '';
        $this->load();

        return True;
    }

    public function load($key=null) {
        if($key) $this->_key = $key;
        if(!isset($this->_key) || $this->_key == '') {
            foreach($this->_attributes as $name=>$value) {
                $this->_attributes[$name] = null;
            }

            return $this;
        }

        $bucket = $this->_client->bucket($this->_domain);
        $obj = $bucket->get($this->_key);
        $data = json_decode($obj->getData());

        if(!$data) return False;

        foreach($data as $key=>$val) {
            // if(!array_key_exists($key, $this->_attributes)) continue;
            $this->set($key, $val);
        }

        $this->_key = $obj->getKey();

        foreach($obj->getLinks() as $link) {
            $this->_links[$link->getTag()][] = array(
                "bucket" => $link->getBucket(),
                "key" => $link->getKey(),
            );
        }

        return $this;
    }

    public function set($key, $val) {
        if(!isset($key) || $key == "") return False;

        $this->_attributes[$key] = $val;
    }

    public function get($key) {
        if(!isset($key) || $key =="") return False;

        return isset($this->_attributes[$key]) ? $this->_attributes[$key] : '';
    }

    public function getDomain() {
        return $this->_domain;
    }

    public function getKey() {
        return $this->_key;
    }

    public function setLink($tagName, $linkObj) {
        if(!is_object($linkObj)) return False;

        $bucket = $linkObj->getDomain();
        $key = $linkObj->getKey();
        $this->_links[$tagName][] = array(
            'bucket' => $bucket,
            'key' => $key,
        );

        return True;
    }

    public function getLink($tagName) {
        if(isset($this->_links[$tagName])) return $this->_links[$tagName];

        return False;
    }

    public function getLinkAs($tagName, $className) {
        if(!isset($this->_links[$tagName])) return False;

        $linkArr = array();
        foreach($this->_links[$tagName] as $tag) {
            $linkArr[] = new $className($tag['key']);
        }

        return $linkArr;
    }

    public function removeLink($tagName, $domain, $key) {
        if(isset($this->_links[$tagName])) {
            foreach($this->_links[$tagName] as $k=>$link) {
                if($link['bucket'] == $domain && $link['key'] == $key) {
                    unset($this->_links[$tagname][$k]);
                    $this->_removedLinks[] = array(
                        'tag' => $tagName,
                        'bucket' => $domain,
                        'key' => $key,
                    );
                }
            }
        }
    }
}
