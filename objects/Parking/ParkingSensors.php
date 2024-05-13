<?php
declare(strict_types=1);
class ParkingSensors
{
    private datetime $lastupdated;
    private datetime $status_timestamp;
    private int $zone_number;
    private string $status_description;
    private int $kerbsideid;
    private float $lon;
    private float $lat;
    private string $img_URL;
    private Address $address;

    public function __construct($lastupdated, $status_timestamp, $zone_number, $status_description, $kerbsideid, $lon, $lat,$img_URL,$address)
    {
        $this->lastupdated = $lastupdated;
        $this->status_timestamp = $status_timestamp;
        $this->zone_number = $zone_number;
        $this->status_description = $status_description;
        $this->kerbsideid = $kerbsideid;
        $this->lon = $lon;
        $this->lat = $lat;
        $this->img_URL = $img_URL;
        $this->address = $address;
    }

    public function getLastupdated()
    {
        return $this->lastupdated;
    }
    public function getStatus_timestamp()
    {
        return $this->status_timestamp;
    }
    public function getZone_number()
    {
        return $this->zone_number;
    }
    public function getStatus_description()
    {
        return $this->status_description;
    }
    public function getKerbsideid()
    {
        return $this->kerbsideid;
    }
    public function getLon()
    {
        return $this->lon;
    }
    public function getLat()
    {
        return $this->lat;
    }
    public function getImg_URL()
    {
        return $this->img_URL;
    }
    public function getAddress()
    {
        return $this->address;
    }
}

