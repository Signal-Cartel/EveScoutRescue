<?php

// check if called from an allowed page
if (!defined('ESRC'))
{
	echo "Do not call the script direct!";
	exit ( 1 );
}

class RescueMail
{
  private $to;
  private $subject;
  private $body;

  public function __construct($to, $subject, $body)
  {
      $this->to = $to;
      $this->subject = $subject;
      $this->body = $body;
  }

  public function getAddressees()
  {
      return $this->to;
  }

  public function getSubject()
  {
      return $this->subject;
  }

  public function getBody()
  {
      return $this->body;
  }

  public function __toString()
  {
    return  "To: " . $this->getAddressees() . "\nSubject: " .
      $this->getSubject() . "\n " . $this->getBody();
  }
}

?>
