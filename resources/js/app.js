require('./bootstrap');
require('./rakuten');
require('./rate_set');
require('./common');
require('./template');
require('./message');

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();
