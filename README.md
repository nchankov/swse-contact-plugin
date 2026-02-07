# SWSE Contact Form Plugin

The plugin provide simple contact form functionality which can be used to send emails to the website owner. 
It uses PHPMailer library to send the emails and it is configured using environment variables.

# Functionality provided by the plugin:
- Render a contact form with fields for name, email, subject and message.
- Validate the form input and display error messages if needed.
- Turnstile integration to prevent spam submissions.
- Send an email to the website owner with the form data using PHPMailer.
- Customizable email template.


## Installation

Download or clone the plugin into your SWSE project:

```bash
cd your-swse-project
git clone https://github.com/nchankov/swse-contact-plugin.git contact
```

Add the required dependencies using composer:

```bash
composer require phpmailer/phpmailer
```

## Configuration

The plugin is configured using environment variables. You can create a `.env` file in the `contact` directory. Use 
the `.env.example` file as a template. Change the values to match your email server configuration and other settings.

## Adding assets into the project

Copy the contents of `contact/assets` directory into your project's `public/assets` directory. This will make the CSS 
and JS files available to the website.

Make sure that they are included in the page header or in the layout file like so:

```html
<link rel="stylesheet" href="/assets/css/contact/form.css">
<script src="/assets/js/contact/form.js"></script>
```

## Adding the contact form to your website

To include the contact form in your website, simply add the following line to the desired page or layout:

```html
<!--include://contact//form.html-->
```
Or if you want to pass custom variables to the form, you can do it like this:

```html
<!--include://contact//form.html ["title" => "Contact Form", "description" => "Please fill out the form below to get in touch with us."]-->
```

This will render the contact form on the page. When the form is submitted, it will send an email to the address 
specified in the configuration.

## Turnstile Integration

Register an account in Cloudflare Turnstile and get your site key and secret key. Then add them to the `.env` file 
in the `contact` directory. This would render and check on submission the turnstile widget to prevent spam submissions. 
If the keys are not set, the verification will be skipped.

## Customization

Copy the contact/views/form.html into your project views and modify it as needed. Then include it instead of the plugin 
form. For example:

```html
<!--include:/contact/form.html-->
```
*Tip* - Note the difference between `//` and `/` in the include path. The first one is used to include files from 
the plugin `//{plugin_name}//`, while the second one is used to include files from the project views.

Modify the css and js files as needed in your product assets directories.

If you want to customize the email template, copy the file specified in `MESSAGE_TEMPLATE_PATH` variable into your 
project and modify it as needed. Then update the path in the `.env` file to point to the new location.