<?php



/**
 * @property help_model $help
 */
class MY_Controller extends Controller{
  /**
   *
   * @var array
   */
  protected $data = array();
  public function MY_Controller(){
    parent::Controller();
    $this->load->library('Markdown');
    $this->load->helper('markdown');
    session_start();
  }
}