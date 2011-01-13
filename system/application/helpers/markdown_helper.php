<?php

function markdown($text){
  static $m;
  if(!$m){
    $m = new Markdown();
  }
  return $m->transform($text);
}
