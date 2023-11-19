/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Dropzone = require('dropzone');
window.Dropzone.autoDiscover = false;

var ace = require('ace-builds/src-noconflict/ace');
var ace_yaml_mode = require('ace-builds/src-noconflict/mode-yaml');

// toastr.js
// https://cylab.be/blog/219/notifications-with-toastrjs-and-laravel
window.toastr = require('toastr');
window.toastr.options = {
  "progressBar": true
};

// prismjs
// https://cylab.be/blog/222/syntax-highlighting-with-prismjs-for-your-laravel-application
import Prism from 'prismjs';

// lightbox
import 'lightbox2';

// chart.js
require('chart.js/dist/Chart.bundle.js');
require('chartjs-plugin-annotation/chartjs-plugin-annotation.min.js');

// Highlight current link in main menu
$(document).ready(function() {
    var current = location.pathname;
    $('#nav-main a').each(function(index, link){
        
        var parser = document.createElement('a');
        parser.href = $(link).attr('href');
        var link_path = parser.pathname;

        if (current === "/") {
            // we are at the homepage...
            if (link_path === "/") {
                $(link).addClass('active');
                return;
            }

        } else {
            if (link_path === "/") {
                return;
            }

            if(current.indexOf(link_path) === 0){
                $(link).addClass('active');
            }
        }
    });
});