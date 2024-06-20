<?php

/**
 * render_template.php
 *
 * Simple renderer for templates
 *
 * @author BP (3komma3volt)
 * @license MIT License
 */



/**
 * @param mixed $template file of template
 * @param mixed $data some date which can be used in the templates
 * 
 * @return output of renderer
 */
function renderTemplate($template, $data)
{
  if (!file_exists($template)) {
    echo 'Template file .$template. not found';
    exit();
  }

  extract($data);
  ob_start();
  include($template);
  $content = ob_get_clean();
  return $content;
}
