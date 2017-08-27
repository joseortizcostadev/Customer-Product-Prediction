# Welcome to Customer Product Predictor (CPP)

This module is an open source contribution to Drupal core and [Drupal.org](https://www.drupal.org) developed by Jose Ortiz <jose@thinkbinario.com>

Download [CPP Module](https://www.drupal.org/project/customer_product_predictor) from Drupal.org or you can also clone this repository in your site drupal instalation path at sites/all/modules/

## What is CPP?

Customer Product Predictor (CPP) is a set of modules designed to allow you to run prediction features on your Drupal site in order to predict the probability of a customer buying your products based on the latest artificial intelligence algorithms and tools. Development has been driven by two major emphases: flexibility and usability. So far, we have launch the core module of this product which is fully functional and independent of future releases related to this product. Also, we have launch the module CPP Ubercart which automatically evaluate order comments made when your customers buy a product using the ubercart module. CPP Module provide custom hooks that are fired when a automatic evaluation of your order is made. That's it, we are building a scalable and flexible system which administrative interface, particularly relating to the configuration of your data and its related permissions has been designed for ease of use and efficient display of information.

For more details, be sure to visit the [What is CPP?](http://www.thinkbinario.com/cpp) page for more info.

## Current Features

* CPP includes CPP Ubercart module
* Supported by Drupal 7.x
* Automatic product and order evaluation based on the comments left by the users who have made a purchase
* CPP custom hooks to promote your products based on a user evaluation order. Useful when customer is not happy about our product or order
* New CPP email api to make communication between your company and your customers easier
* Drupal administration components such as CPP settings and detailed product-order automatic evaluation table
* Activity logging
* Much more... and more to come.

## Support and Issue Tracking:

* [User's and developer's guides](http://www.thinkbinario.com/cpp/docs)
* [Installation manual with modules descriotion](http://www.thinkbinario.com/cpp/installation)
* [Support forums](http://www.thinkbinario.com/cpp/support)
* [Report a bug or feature](http://drupal.org/project/issues/customer_product_predictor)

## CPP and CPP Ubercart Installation

### Release Info

* Created by: Jose Ortiz founder of [ThinkBinario](www.thinkbinario.com)
* Created on: 25 Aug 2017 at 04:06 UTC
* Last updated: 25 Aug 2017 at 04:19 UTC
* Core compatibility: 7.x

### Release Notes
* Required
    - ubercart 7.x-3.10
    - rules 7.x-2.10
    - entity 7.x-1.8
    - views 7.x-3.18
    - ctools 7.x-1.12
* Optional
    - No optional projects
   
### Download Instructions
* You can download CPP modules from the following links.
    - CPP and CPP Ubercart releases may be downloaded from [Drupal.org](https://www.drupal.org/project/customer_product_predictor) 
    - CPP and CPP Ubercart releases also may be downloaded directly from [CPP Github repository]() 

### Installation
After downloading CPP modules from any of the provided links. You need to the costumer_product_predictor folder in the following path /sites/all/modules/ located in your drupal installation folder.

Once the main CPP module is placed on the correct path enable the modules Customer Product Prediction and CPP Ubercart. Remeber to flush caches from drupal and your bronwers after module installation. If everything went as expected, your are all set up

At this point, your data has been already trained (this happens during installation time), and for every new order comment placed by a customer, you'll be able to see its automatic evaluation in CPP Ubercart Featured administration table. Not to mention that you won't be able to see this data until you have at least 10 or more orders in placed in your system. Also, remember that you can implement CPP custom hooks to enhance your CPP experience. 

