<?php
class EmailTemplate
{
    protected $variables;
    protected $path_to_file;
    function __construct($path_to_file)
    {
         if(!file_exists($path_to_file))
         {
             trigger_error('no email template found',E_USER_ERROR);
             return;
         }
         $this->path_to_file = $path_to_file;
    }

    public function __set($key,$val)
    {
        $this->variables[$key] = $val;
    }


    public function compile()
    {
        ob_start();

        extract($this->variables);
        include $this->path_to_file;


        $contents = ob_get_contents();
        ob_end_clean();
				
        return $contents;
    }
}
