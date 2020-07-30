/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import '../css/style.css';
import '../css/login.css';
import '../css/buttons.css';
import '../css/forms.css';
import '../css/painel.css';


// JS
import '../js/main.js';
import '../js/painel.js';

// Vendors
 import '../vendor/icofont/icofont.min.css';
 import '../vendor/boxicons/css/boxicons.min.css';
 import '../vendor/animate.css/animate.min.css';
 import '../vendor/venobox/venobox.css';

 import '../vendor/jquery.easing/jquery.easing.min.js';
 import '../vendor/jquery-sticky/jquery.sticky.js';
 import '../vendor/venobox/venobox.min.js';

// Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import 'bootstrap/dist/js/bootstrap.min.js';

// Font Awesome
import '@fortawesome/fontawesome-free/js/fontawesome';
import '@fortawesome/fontawesome-free/js/solid';
import '@fortawesome/fontawesome-free/js/regular';
import '@fortawesome/fontawesome-free/js/brands';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.
global.jQuery = global.$ = require ('jquery'); 

console.log('Hello Webpack Encore! Edit me in assets/js/app.js');
