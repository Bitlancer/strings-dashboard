<?php

App::uses('ExceptionRenderer', 'Error');
App::uses('CakeResponse', 'Network');

class AppExceptionRenderer extends ExceptionRenderer {

    public function __construct(Exception $e){
        $this->exception = $e;
        $this->response = new CakeResponse();
    }

    public function render(){

        $exception = $this->exception;
    
        $debug = Configure::read('debug');

        $statusCode = 0;
        $errorMessage = "";

        //Set status code and error message
        if($exception instanceof NotFoundException || 
            $exception instanceof MissingControllerException ||
            $exception instanceof MissingActionException){

            $statusCode = 404;
            $errorMessage = 'Not Found';
        }
        elseif($exception instanceof ForbiddenException){
            $statusCode = 403;
            $errorMessage = 'Forbidden';
        }
        elseif($exception instanceof BadRequestException){
            $statusCode = 400; 
            $errorMessage = 'Bad Request';
        }
        else {
            $statusCode = 500;
            $errorMessage = 'Internal Server Error';
        }

        if($debug){
            $errorMessage = $exception->getMessage();
        }


        //Select template
        $template = 'default';
        if($debug){
          $template = 'debug';
        }

        $viewData = array(
            'statusCode' => $statusCode,
            'errorMessage' => $errorMessage,
            'exceptionClass' => get_class($exception),
            'line' => $exception->getLine(),
            'file' => basename($exception->getFile()),
            'trace' => $exception->getTraceAsString()
        );

        $this->response->type('html');
        $this->response->statusCode($statusCode);
        $this->response->body($this->generateHtml($template,$viewData));
        $this->response->send();
    }

    private function generateHtml($template,$viewData=array()){

        return call_user_func(array($this,"template_$template"),$viewData);
    }

    private function template_default($viewData=array()){

        extract($viewData);
        
        $src = <<<EOD
<!doctype html>
<html lang="en-US">
<head>
  <link rel="stylesheet" type="text/css" href="/css/app-error.css" />
</head>
<body>
  <div>
    <h1>Oops!</h1>
    <h3>$statusCode - $errorMessage</h3>
  </div>
</body>
</html>
EOD;

        return $src;
    }

    private function template_debug($viewData=array()){

        extract($viewData);
        
        $src = <<<EOD
<!doctype html>
<html lang="en-US">
<head>
  <link rel="stylesheet" type="text/css" href="/css/app-error.css" />
</head>
<body>
  <div>
    <h1>Oops!</h1>
    <h2>$statusCode - $errorMessage</h2>
    <h3>$file ($line)</h3>
    <h3>$exceptionClass</h3>
    <pre class="wrap">$trace</pre>
  </div>
</body>
</html>
EOD;

        return $src;
    }
}
