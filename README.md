# Testing application for OpenID Connect with OpenAM
> **Attention!** This application is only for testing. Do not use as Relying Party.


With this application you can do:
 - [Authentication using OIDC Authorization Code Flow](http://openid.net/specs/openid-connect-core-1_0.html#CodeFlowAuth).
 - [Authentication using OIDC the Implicit Flow](http://openid.net/specs/openid-connect-core-1_0.html#ImplicitFlowAuth).

It has only been tested with [OpenAM](https://forgerock.org/openam/) as an [OIDC provider](https://wikis.forgerock.org/confluence/display/openam/OpenID+Connect+Quick+Start).

#### Application Settings
  - Edit file "utils/config.php"
    - Put the URL of your OIDC IdP:
      
      ```php
        $config = [
            'IdP' => [
        		'iss' => 'https://sample.idp.com/openam/oauth2',
            ],
            ...
        ];
      ```
    
    - Put the scope you want to retrieve:
      
      ```php
        $config = [
            'request' => [
        		'scope_params' => 'openid email',
        		...
            ],
            ...
        ];
      ```
   
   - Enter the identifier and password assigned to this RP:
      
      ```php
        $config = [
            'request' => [
        		'client_id' => 'myRP',
                'client_secret' => 'test',
        		...
            ],
            ...
        ];
      ```
   
   - Put the redirect URI in the implicit flow (redirect_i) and the Authorization Code (redirect_c):
      
      ```php
        $config = [
            'request' => [
        		'redirect_c' => 'https://myRP.com/',
		        'redirect_i' => 'http://myRP.com/',
        		...
            ],
            ...
        ];
      ```

#### Installation
 - Put the source code of this application on your PHP server.
 - Configure the application.
 - Configure your IdP. If you use OpenAM, you can take a look at [this page](https://wikis.forgerock.org/confluence/display/openam/OpenID+Connect+Quick+Start).

### Todo
 - Exception control.
 - OIDC discovery and registration.
 - Include phpseclib library with Composer.


### Development
Want to contribute? Great! You're welcome.



**It's Free!**
