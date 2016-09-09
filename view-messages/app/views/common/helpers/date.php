<?php

class DateHelper extends TimeHelper {

	var $dateObj = null;

	function nice($date) {
		vendor('Date');

		$this->dateObj = new Date($date);
		$this->dateObj->setTZByID("EST");
		$this->dateObj->convertTZByID("IST");
		return date("D, M j Y, G:i",strtotime($this->dateObj->getDate()))." IST";
	}
}
?>