# M3 Framework


[![Join the chat at https://gitter.im/M3framework/m3](https://badges.gitter.im/M3framework/m3.svg)](https://gitter.im/M3framework/m3?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

**M3** is a PHP framework for fast developing web applications using the MVC design pattern.

M3 doesn't requiere root privileges for installing it, so a M3 application runs smoothly on a shared server like Cpanel. You can even have several M3 applications in separated directories, each one with its own M3 version.

**WARNING**: M3 is in a **very-very-alpha** stage of development. Many parts are incomplete, and the API can change in any moment. For now, it's not suitable for production environments. Use it at your own risk.

# Requirements

* PHP 5.6 or 7+ with `CLI`, and modules `mbstring` and `fileinfo` installed.

# Quickstart 

* Clone this repo inside a directory included in the [include_path](http://php.net/manual/en/ini.core.php#ini.include-path) PHP directive.

```
git clone -b dev git@github.com:M3framework/m3.git
```
If you doesn't have access to any of the `include_path` directories nor alter the PHP directive value, just clone it anywhere, and set the environment variable M3_BASE_PATH with its full path

```
cd /home/oliver
git clone -b dev git@github.com:M3framework/m3.git
export M3_BASE_PATH=/home/oliver/m3
```

* Set up access to the `m3` administration script.

You can either create a symbolic link inside a directory listed in the `PATH` environment variable:

```
ln -s path/to/the/m3/bin/m3 /usr/local/bin
```

Or you can alter the `PATH`:

```
export PATH=path/to/the/m3/bin:$PATH
```

* Create a new M3 project with the command `m3 init`.

```
m3 init myapp
```

This will create a `myapp` directory with a basic project structure.

* Launch a development server:

```
cd myapp
m3 server
```

Point your web browser to http://localhost:8888 and you're ready to go.

# Documentation

It's nonexistent :sweat_smile: I'm working on that. You can ask me question on the [Gitter chat](https://gitter.im/M3framework/m3).

# About the author

My name is [Oliver Etchebarne](http://drmad.org), from [Ica](https://en.wikipedia.org/wiki/Ica,_Peru), [Perú](https://en.wikipedia.org/wiki/Peru). I started (indirectly) coding this framework in the year 2000, building several libraries for access the database, html forms render and validating, etc.

*Circa* the year 2012 I started to find another language for my web apps, and tried Django and Rails, but neither really convinced me. Next year, I "discovered" that PHP was *[less ugly](https://drmad.org/blog/10-cosas-que-probablemente-no-sabias-de-php.html)*, so I gave it a new try, updating and integrating all my libraries (and creating new ones inspired on Django y Rails :grin:) in this framework base. 

Two years ago (2014), I decided to polish all the libraries for publishing the framework as an Open Source project, and begun to close the gaps in the integration of all, and gave its name "M3". This year (2016) I gave it the last *overhauling* using the [PHP-FIG](http://www.php-fig.org/) guidelines, and updating the objects and classes for more loose-coupling between then, and implementing some other new coding paradigm.

And today, September 17, 2016, to celebrate the [Software Freedom Day](http://www.softwarefreedomday.org/), I finally published it to the GitHub :smiley: .

A more long (and in spanish) version of this story can be found in my blog: https://drmad.org/blog/m3-framework.html.

Hope to hear from you soon!
