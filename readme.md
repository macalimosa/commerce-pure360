## Commerce Pure360
Commerce pure360 is build for easy implementation of [Pure360](https://app.pure360.com) api to [Commerce](https://docs.modmore.com/en/Commerce/v1/index.html) modmore plugin

### Register Module
In order for the modules to show up in that list, it must first be registered. [Register module docs](https://docs.modmore.com/en/Commerce/v1/Developer/Modules/index.html)
#### Snippets
```
[[!commerceRegisterModule? 
 &directory=`components/commerce/src/Modules/Pure360/` 
 &namespace=`modmore\Commerce\Modules\Pure360\`
]]
```
**Parameters**

|Name|Type|Description|
|---|---|---|
| directory|  string:required| Module directory will be merge to MODX_CORE_PATH  |
| namespace|  string:required|Class namespace |

this sample snippets will be found [here](snippets/commerceRegisterModule.snippet.php)

### Module form interface

To update the form module label, you will need to create Lexicon from  Settings -> Lexicons -> Create Entry

|Name|Value|
|---|---|---|
| commerce.module.pure360.accept_marketing.text|  I would like to receive email updates and the occasional newsletter.  |
| commerce.module.pure360.account_name| Account name |
|commerce.module.pure360.account_name.description||
|commerce.module.pure360.class_name|Class name|
|commerce.module.pure360.class_name.description|Class name of pure360 module|
|commerce.module.pure360.class_path|Class path|
|commerce.module.pure360.class_path.description|Path of Pure360 class module|
|commerce.module.pure360.custom_fields|Custom fields|
|commerce.module.pure360.custom_fields.description|Format:pure360field:addresskey e.g name:fullname,telephone:phone,company:company,products:products|
|commerce.module.pure360.double_optin|Double Optin|
|commerce.module.pure360.double_optin.description||
|commerce.module.pure360.list_name|List name|
|commerce.module.pure360.list_name.description||

You can update the values whatever you like.

### Display Checkbox for signup

```
{% if module_pure360_marketing_enabled %}
  <label class="c-accept-newsletter">
    <input type="checkbox" name="accept_marketing" value="1">
      {{ lex('commerce.module.pure360.accept_marketing.text') }}
    </label>
{% endif %}
```

Add this codes to your custom template twig

please see [Front-end Theming](https://docs.modmore.com/en/Commerce/v1/Front-end_Theming.html) how to setup your Theme  



