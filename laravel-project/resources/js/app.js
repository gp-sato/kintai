import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const name = document.getElementById('name');
const email = document.getElementById('email');
const btn_clear = document.getElementById('btn_clear');

if (btn_clear) {
  btn_clear.addEventListener('click', function(event) {
    if (name) name.value = '';
    if (email) email.value = '';
  });
}
