<?php
namespace M3;

use M3;
use M3\Http;

/**
 * Decorator-like class to add M3 processing classes to the View
 */
class View extends View\Php
{
    protected function getCanonicalFilePath($file, $type = 'view', 
        &$application = null)
    {

        if ($type == 'view') {
            $type = 'views';
        } elseif ($type == 'layout') {
            $type = 'views/layouts';
        }

        if ($file instanceof M3\Path\FileSearch) {
            $path = $file;
        }
        else {
            $path = new Path\FileSearch($file, $type);  
        }

        if ($path->found()) {
            return $path->get();
        } else {
            throw new M3\Exception("View file '$file' not found.", [
                'Searched path' => $path->searched_paths,
            ]);

        }
    }

    /**
     * Render this view applying the layout. Returns a M3\Http\Response
     */
    public function renderToResponse()
    {
        return new Http\Response($this->renderToString());
    }

    /**
     * Overloaded constructor for creating a new security token
     */
    public function __construct($file = null, $variables = null)  
    {
        (new Csrf)->generateToken();
        parent::__construct($file, $variables);
    }

    /**
     * Shortcut for rendering a view
     *
     * @return object M3\Http\Response
     */
    public static function render($view_file, array $variables = [])
    {
        $view = new static($view_file, $variables);
        $content = $view->renderToString();
        return new Http\Response($content);
    }

    /** Base name of the view */
    //public $name = '';

    /**
     * Set the view file.
     */
    /*public function file($view)
    {
        $file = new Path\FileSearch($view, 'views');
        if ($file->notFound()) {
            throw new NotFoundException("View file '$view' not found.", [
                'Searched paths' => $file->searched_paths
            ]);
        }
        $this->file = $file->get();
        $this->name = $view;
    } */

    /**
     * Set the layout file
     */
    /*public function layout($layout)
    {
        // Ya que el layout se busca varias veces, $layout puede ser
        // un objeto M3\FilePath. Lo reusamos
        if ($layout instanceof Path\FileSearch) {
            $file = $layout;
            $layout = $file->file;
        } else {
            $file = new Path\FileSearch($layout, 'views/layouts');
        }

        if ($file->notFound()) {
            throw new NotFoundException("Layout '$layout' not found.", [
                'Searched paths' => $file->searched_paths
            ]);
        }

        $this->layoutFile = $file->get();
    }*/

    /*public static function render($view_file, array $variables = []) 
    {
        $file = new Path\FileSearch($view_file, 'views');
        if ($file->notFound())  {
            throw new NotFoundException("View '$view_file' not found.", [
                'Searched paths' => $file->searched_paths
            ]);
        }
        $view_file = $file->get();

        return parent::render($view_file, $variables);
    }*/
}
