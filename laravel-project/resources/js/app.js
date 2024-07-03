import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const name = document.getElementById('name');
const email = document.getElementById('email');
const btn_clear = document.getElementById('btn_clear');

btn_clear.addEventListener('click', function(event) {
  name.value = '';
  email.value = '';
});
