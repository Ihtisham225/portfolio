import './bootstrap';

// Load jQuery FIRST and expose globally
import $ from 'jquery';
window.$ = window.jQuery = $;

// FORCE Select2 to attach to global jQuery
import select2 from 'select2';
select2(window.$);

// Select2 styles
import 'select2/dist/css/select2.css';

// Page-specific JS
import './projects';

// Page-specific JS
import './posts';

// Alpine
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();
