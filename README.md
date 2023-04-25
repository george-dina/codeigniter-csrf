Codeigniter 3 has an issue with CSRF validation if the request is intercepted and resubmitted. Basically, in each request, if the cookie and hidden CSRF post are the same, the request will be submitted.

In order to actually validate server side the value, in this example the CSRF token is stored in session and reset after the submit to prevent the above vulnerability.

##### 1. Add another  config parameter to allow extra configuration.
In `application/config/config.php` add a new config option.
>... 
$config['csrf_protection'] = true;
**$config['real_csrf_protection'] = true;**
...

##### 2. Enable hooks
Since CI3 cannot extend easily the Security class due to the moment when it's initialized, we will not be doing that fully, we will just validate another time the CSRF token against session data.
 Enable Hooks in  `application/config/config.php` 
> $config['enable_hooks'] = true;

##### 3. Define two new hooks:
The one that will validate against the session will be initialized after the CI_Controller class is initialzed but before the construct finished executing.

The other one will run before any system code is executed to prevent removing the CSRF token from $_POST (which is done in Security class, on csrf_verify call)

```php
$hook['post_controller_constructor'][] = array(
  'class' => 'MY_Security',
  'function' => 'csrf_verify',
  'filename' => 'MY_Security.php',
  'filepath' => 'hooks'
);
$hook['pre_system'][] = array(
  'class' => 'MY_Preserve_post',
  'function' => 'keep_csrf_fields',
  'filename' => 'MY_Preserve_post',
  'filepath' => 'hooks'
);
```

##### 4. Add the hooks files 
Place the files from this repo in `application/hooks` folder
