M3.ajax = {
	req: {},

	/**
	 * Prepara un formulario para ser enviado por ajax.
	 */
    prepare: function (form, callback)
    {
        if (typeof form == "string") {
            var f = M3.id(form)
        } else {
            var f = form 
        }
        
        f.onsubmit = M3.ajax.submit
        f.callback = callback
    },

	message: function (message, title = '')
    {
	    div = M3.id('m3_ajax_message');

	    // Si no hay div, usamos un window.alert 
	    if (!div) {
	        window.alert (message)
	        return false
	    }

	    if (message == false) {
	        div.style.display = 'none';
	        return true;
	    }
	    
	    // Mostramos siempre el mensaje    
	    div.style.display = 'block';
	    
	    div.innerHTML = msg;
	},

    /** 
     * Activa o desactiva el elemento #m3_ajax_progress, donde puede
     * haber un icono de progreso del ajax
     */
    progress: function (active)
    {
        progress = M3.id('m3_ajax_progress')

        if ( progress ) {
            if ( active ) {
                progress.style.visibility = 'visible';
            } else {
                progress.style.visibility = 'hidden';
            }
        }
    },

	call: function (method, url, vars, callback)
    {
	    if(window.XMLHttpRequest) {
	        try {
	            req = new XMLHttpRequest();
	        } catch(e) {
	            req = false;
	        }
	    } else if(window.ActiveXObject) {
	        try {
	            req = new ActiveXObject("Msxml2.XMLHTTP");
	        } catch(e) {
	            try {
	                req = new ActiveXObject("Microsoft.XMLHTTP");
	            } catch(e) {
	                req = false;
	            }
	        }
	    }

        // De repente no hay variables, y es el callback
        if (typeof(vars) == "function") {
            callback = vars
            vars = {}
        }


	    // Convertimos las variables en un string
	    res = [];
	    for ( v in vars ) {
            // Los arrays lo tratamos distinto
            if (vars[v] instanceof Array) {
                for (d in vars[v]) {
                    res.push ( v + '[]=' + encodeURIComponent ( vars[v][d] ) );
                }
            }
            else
              res.push ( v + '=' + encodeURIComponent ( vars[v] ) );
	    }
	    vars = res.join ('&')

	    // Si llamamos por GET, las variables las ponemos en la URL
	    if ( method == "GET" ) {
	    	url += '?' + vars;
        }

	    // Obtenemos la ruta base. Si no tiene schema, agregamos la base
	    re = /^.*:\/\//
		if (!re.exec (url)) {
			url = M3.URLBASE + url
        }

        // Activamos el div del progress
        M3.ajax.progress (true)

	    req.open (method, url, true)

    	// Marca de AJAX
	    req.setRequestHeader('X-M3-Requested-With', 'XmlHttpRequest');

        // Le añadimos el Token de CSRF
        req.setRequestHeader('X-M3-Security-Token', 
            M3.e("meta[name=m3-security-token]").content);

	    // La información va urlencodeadea
	    req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
	    
	    req.onreadystatechange = function (data) {

	        if (this.readyState == 4) {
                // Desactivamos el progress
                M3.ajax.progress ( false )
	            try {
                    if (JSON.parse) {
                        data = JSON.parse ( this.responseText )
                    } else {
                        data = new Function('return ' + this.responseText + ';')();
                    }

	                // Nos fijamos si viene algun código de error
	                switch ( data.__CODE ) {
	                case -255: // Message
	                    M3.ajax.message (data.__MESSAGE)
                        break
                    case -65535:
                        // Execption!
                        d = M3.c('div');
                        d.style.position = 'fixed';
                        d.style.top = "50px"
                        d.style.left = "50px"
                        d.style.width = (document.width - 100)  + "px"
                        d.style.height = (document.height - 100) + "px"
                        d.style.background = 'white'
                        d.style.border = "1px solid black"
                        d.style.padding = "10px";
                        d.style.zIndex = "100";

                        M3.e('body').appendChild ( d )

                        d.innerHTML = data.__MESSAGE

                        console.log ( data.__MESSAGE )
                        console.log ( d )
                    }

	                // retornamos el array data a la funcion callback
	                callback (data);
	                
	                return;
	            }
	            catch (e) {
	                //ajax.message ('¡Error inesperado!');

	                console.log ('ERROR ' + e.name + ": " + e.message );
	                console.log ( e.stack );
	                console.log( {responseText : this.responseText});
	                return;
	            }
	        }
	    }
	    req.send (vars);
	},

	post: function (url, vars, callback)
    {
		M3.ajax.call ( 'POST', url, vars, callback );
	},

	get: function (url, vars, callback)
    {
		M3.ajax.call ( 'GET', url, vars, callback );
	},

    submit:function(evt)
    {

        //Evitamos que el formulario funcione
        evt.preventDefault()

        form = this;

        // Convertimos cada elemento del formulario para enviarlo por ajax
        vars = {}
        for ( var i = 0; i < form.elements.length; i++ ) {
            el = form.elements [ i ]


            // Según el tipo de control, sacamos su valor.
            value = null
            switch ( el.nodeName.toLowerCase() ) {
            case 'input':
                switch ( el.type ) {
                case 'checkbox':
                    // El checkbox es algo especial
                    value = el.checked?el.value:''
                    break;
                default:
                    value = el.value
                }
                break;
            default:
                value = el.value
            }

            if ( value ) {
                // Si ya existe este elemento, lo añadimos al array
                if ( vars [ el.name ] ) {

                    // Primero, tenemos que convertirlo en un array, si
                    // no lo es
                    if ( ! ( vars [ el.name ] instanceof Array) ) {
                        vars [ el.name ] = [ vars [ el.name ] ]
                    }

                    vars [ el.name ].push  ( el.value  )
                } else {
                    vars [ el.name ] = el.value 
                }
            }
        }

        // Si no hay action, usamos la URL actual
        if ( !form.action ) {
            form.action = document.location.href
        }

        M3.ajax.call ( form.method, form.action, vars, form.callback )
    },
}
