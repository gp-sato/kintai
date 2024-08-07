import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const name = document.getElementById('name');
const email = document.getElementById('email');
const password = document.getElementById('password');
const passwordConfirm = document.getElementById('password-confirm');
const btn_clear = document.getElementById('btn_clear');
const delete_form = document.getElementById('delete_form');
const btn_delete = document.getElementById('btn_delete');

if (btn_clear) {
  btn_clear.addEventListener('click', function(event) {
    if (name) name.value = '';
    if (email) email.value = '';
    if (password) password.value = '';
    if (passwordConfirm) passwordConfirm.value = '';
  });
}

if (delete_form && btn_delete) {
  btn_delete.addEventListener('click', function(event) {
    if (!confirm('このユーザーを削除します。よろしいでしょうか？')) {
      return;
    }
    delete_form.submit();
  });
}
