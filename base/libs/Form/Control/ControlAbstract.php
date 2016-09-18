<?php
namespace M3\Form\Control;

use M3;
use M3\Html;
use M3\Form;
use M3\Form\Validator;

abstract class ControlAbstract implements M3\Database\ValueInterface {

    /** Parent form object */
    protected $form = null;

    /** Messages for this control */
    protected $messages = [];

    /** Default properties */
    protected $properties = [
        /** false disable this control entirely */
        'enabled'        => true,

        /** true if this control must be validated */
        'validate'      => true,

        /** Caption. If is null, then the control name will be used */
        'caption'       => null,

        /** Extra information of the controller */
        'info'          => '',

        /** Values list, for use of some controls. */
        'list'          => [],

        /** Array of filter functions. as in Form\Filter class*/
        'filters'       => [],

        /** Array of validating functions, as in Form\Validator class */
        'validators'    => [],

        /** true if a empty value is allowed */
        'empty_allowed' => false,

        /** Message if empty and empty is not allows */
        'empty_message' => 'This control cannot be empty.',

        /** true if this control must be drawn */
        'draw_control'  => true,

        /** true if the label block must be drawn */
        'draw_label'    => true,

        /** true if this controls returns a value with Form::getValues() */
        'return_value'  => true,

        /** Name of this control when returning values */
        'field_name'    => null,

        /** Callback for extra validation */
        'validator_callback' => null,

        /** Callback for drawing this control */
        'drawer_callback' => null,

        /** Extra HTML properties for the widget tag */
        'html'    => [],

        /** Is this control valid? */
        'valid'         => null,

        /** Processed value */
        'value'         => null,

        'showvalue'     => true,
        'showmessage'   => true,

        //** true when this control is already validated */
        'validated'     => false, 
    ];

    /**
     * Propiedades extra y sus valores por defecto de cada tipo de campo.
     */
    protected $extra_properties = [];


	/**
	 * Al crear un nuevo objeto del campo, los parámetros del constructor
	 * son las propiedades de este elemento.
	 */
	function __construct ($name, $properties, $form) {

        // Este control tiene propiedades por defecto?
        if ( $this->extra_properties ) {
            $this->properties = array_merge( $this->properties, $this->extra_properties );
        }


        $this->name = $name;
        
        // Mezclamos las propiedades con las propiedades por defectos
        $this->properties = array_merge( $this->properties, $properties );

        // Si no tiene un caption, le creamos uno
        if (is_null($this->caption)) {
            $this->caption = ucfirst (strtolower( strtr($name, '_', ' ')));
        }

        $this->form = $form;

	}

    /**
     * M3\Html\Tag wrapper for adding this control's 'html_extra' properties
     */
    function htmlTag ($tagname, $vars = [], $content = null, $options = []) 
    {
        $vars = array_merge($this->html, $vars);
        
        // Siempre evitamos que escape el contenido
        $options ['escapecontent'] = false;
        $options ['escapevariables'] = false;

        return new Html\Tag($tagname, $vars, $content, $options);
    }



    /**
     * Draw the label block
     */
    public function drawLabel ($add_sufix = true) 
    {
        // Si caption es vacío, retornamos nada
        if ( $this->caption ) {

            $sufix = '';
            if ( $add_sufix ) {
                $sufix = $this->form->getProperty('label_sufix');
            }

            return (string)Html\Tag::label (
                    [ 'for' => $this->id() ],
                    $this->caption . $sufix
                );
        }
    }

    /**
     * Draw the message block
     */
    public function drawMessages() 
    {
    	$html = '';

        $tag = $this->form->getHtmlProperty('message');

    	if ($this->messages) {
            foreach ( $this->messages as $message ) {

                $tag[2] = $message;
        		$html .= Html\Tag::create($tag)
                    ->noEscapeContent()
                    ->get()
                ;
        	}
        }

        // Si hay un tag que rodea a todos los mensajes:
        $tag = $this->form->getHtmlProperty('message_block');

        // Si no hay un tag, entonces no dibujamos nada
        if (!$tag) {
            return '';
        }

    
        $tag['id'] = $this->form->getHtmlProperty('message_block_id_prefix') . $this->id();
        $tag[2] = $html;

        //$html = Html\Tag ( $tag, [], ['escape_content' => false] );
        $html = Html\Tag::create($tag) 
           ->closeTag() 
           ->noEscapeContent()
           ->get();

    	return $html;
    }

    /**
     * Adds messages to this control message list
     */
    public function addMessage($messages)
    {

        // Los strings los convertimos en array
        if ( is_string ( $messages )) {
            $messages = [ $messages ];
        }

        foreach ( $messages as $message ) {
        	$this->messages[] = $message;
     
            // Guardamos el mensaje en el formulario
            $this->form->addMessage($this->name, $message);

        }
    }

    /**
     * Returns if this control is empty.
     */
    public function isEmpty() 
    {
        return empty($this->value);
    }

    /**
     * Returns if this control is valid.
     */
    public function isValid()
    {
        // Si no hemos validado, validamos
        if (is_null($this->valid)) {
            $this->validate();
        }

        return $this->valid;
    }

    /**
     * Validates a control using the validators
     */
    public function validate()
    {
        // Sólo validamos una vez
        if (!is_null($this->valid)) {
            return;
        }

        $valid = true;

        if (!$this->empty_allowed) {
            // Añadimos un validador
            array_unshift ($this->properties['validators'], [
                Validator\IsEmpty::class, ['messages' => 
                    ['empty' => $this->empty_message]
                ]
            ]);
        }

        foreach ($this->validators as $validator) {
            $args = [];


            if (is_string($validator)) {
                $class = $validator;
            } else {
                $class = array_shift($validator);
                $args = $validator;
            }

            // Existe la clase?
            if (!(class_exists($class) 
                && is_subclass_of($class, 'M3\Form\Validator\ValidatorAbstract'))) { 

                throw new \RuntimeException("'$class' is not a valid control validator class.");
            }
            
            $v = new $class($this, $args);

            if (!$v->validate())  {
                $this->addMessage($v->getMessages());

                $valid = false;
                break;
            }
        }

        $this->valid = $valid;
        return $valid;
    }

    /**
     * Simple setter for this control's value.
     */
    public function setValue($value)
    {
        // Ejecutamos todos los filtros
        foreach ($this->filters as $filter) {
            $args = [$value];

            if(is_array($filter)) {
                $function = array_shift($filter);
                $args = array_merge($args, $filter);
            } else {
                $function = $filter;
            }

            // La función no existe.
            if (!is_callable($function)) { 
                throw new \RuntimeException("'$function' is not a valid field filter.");
            }

            $value = call_user_func_array($function, $args);
        }

        $this->value = $value;
    }

    /**
     * Simple getter for this control's value.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Simple list setter for this control
     */
    public function setList (array $list)
    {
        $this->list = $list;
    }

    /**
      * Returns this control's ID
      */ 
    public function id ()
    {
        return $this->form->getHtmlProperty('id_prefix') . $this->name;
    }

    /**
     * Returns this control form
     */
    public function getForm() {
        return $this->form;
    }

	/**
	 * Al llamar al objeto como función, llama a la función draw();
	 */
	function __invoke () {
		return $this->draw();
	}

	/**
	 * Al leer el objeto como string, retornamos su valor
	 */
	function __toString() {
        return $this->getValue();
	}
    /**
     * Funciones mágicas para manejar $this->properties
     */
    function __set ($prop, $value) {
    	$this->properties[$prop] = $value;
    }

    function __get ($prop) {
        if (!array_key_exists($prop, $this->properties)) {
           throw new \RuntimeException  ("Unknow '$prop' property.");
        }

    	return $this->properties [$prop];
    }
    function __isset ($prop) {
        return array_key_exists ($prop, $this->properties);
    }

    /**
     * Implementation of M3\Database\ValueInterface
     */
    public function getDatabaseValue(\M3\Database\ConnectorInterface $connector) 
    {
        return $connector->escape($this->getValue());
    }


    /**
     * FUNCIONES ABSTRACTAS
     */


    // Retorna el HTML que dibuja al control
    abstract function draw();
}
