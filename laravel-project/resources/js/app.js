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
const delete_attendance_forms = document.querySelectorAll('.delete_attendance_form');

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

if (delete_attendance_forms) {
  delete_attendance_forms.forEach(element => {
    element.querySelector('.delete_attendance_btn')?.addEventListener('click', function(event) {
      if (!confirm('この勤怠を削除します。よろしいでしょうか？')) {
        return;
      }
      element.submit();
    });
  });
}
