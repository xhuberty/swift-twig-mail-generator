[![Build Status](https://travis-ci.org/thecodingmachine/swift-twig-mail-template.svg?branch=master)](https://travis-ci.org/thecodingmachine/swift-twig-mail-template)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/swift-twig-mail-template/badge.svg?branch=master&service=github)](https://coveralls.io/github/thecodingmachine/swift-twig-mail-template?branch=master)


# Swift Twig Mail Generator

This package is a twig implementation of the [thecodingmachine/swift-mail-template-interface](https://github.com/thecodingmachine/swift-mail-template-interface).

## Installation

```
composer require thecodingmachine/swift-twig-mail-template
```

Once installed, you can start creating instance of the `SwiftTwigMailTemplate` class.

The `SwiftTwigMailTemplate` represents a mail template that can generate Swift mails.

## Example

Because we consider that an example is better than everything else...

Start by creating your mail template. Your template should have two blocks:

```
{% block subject %}
    Your suject
{% endblock %}

{% block body_html %}
    Body with HTML.
{% endblock %}
```

If you want you can add another block containing your text body. This block is optional since we can get your the text body directly from the html one.

```
{% block body_text %}
    Body without HTML.
{% endblock %}
```

Now, let's create a `SwiftTwigMailTemplate` instance. This object will generate a `SwiftMail` from the twig template.

```
// We assume that $twigEnvironment is a valid TwigEnvironment instance
$twigSwiftMailTemplate =  new SwiftTwigMailTemplate($twigEnvironment, 'path/to/template.twig');

// The renderMail method generates a Swift mail object.
$swiftMail = $twigSwiftMailTemplate->renderMail(['paramKey' => paramValue]);

// We fill the swift mail with additional information
$swiftMail->setFrom('sender@example.com');
$swiftMail->setTo('recipient@example.com');

// We assume that $mailer is a valid Swift_Mailer instance
$mailer->send($swiftMail);
```

### Going further

The `SwiftTwigMailTemplate` class is design for dependency injection. You have the possibility to set a deeper configuration for your mail that's why you have access to a bunch of method for:
* setting the from address 
* setting the from name 
* setting the to address 
* setting the to name 
* setting the Bcc address 
* setting the Bcc name 
* setting the Cc address 
* setting the Cc name 
* setting the ReplyTo address 
* setting the ReplyTo name 
* setting the max line size
* setting the priority
* setting the read receip to
* setting the return path

