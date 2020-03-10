<?php

class Database
{

    private $data = [];

    public function __construct()
    {
        if (!file_exists(DATABASE_FILE)) {
            $this->write($this->data);
        } else {
            $this->data = json_decode(file_get_contents(DATABASE_FILE), true);
        }

    }

    public function hasChannel($uid)
    {
        if ($this->getChannel($uid) === null) {
            return false;
        }

        return true;
    }

    public function getChannel($uid)
    {
        foreach ($this->data as $key => $value) {
            if ($value['uid'] === $uid) {
                return $value['cid'];
            }

        }
    }

    public function writeChannel($cid, $uid)
    {
        $this->data[] = ['cid' => $cid, 'uid' => $uid];
        $this->write($this->data);
    }

    public function deleteChannel($cid)
    {
        foreach ($this->data as $key => $value) {
            if ($value['cid'] === $cid) {
                unset($this->data[$key]);
                $this->write($this->data);
            }
        }
    }

    private function write(array $data)
    {
        file_put_contents(DATABASE_FILE, json_encode($data));
    }
}
