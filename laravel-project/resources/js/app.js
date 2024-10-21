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
const show_password_button = document.getElementById('show-password-button');
const show_password_confirm_button = document.getElementById('show-password-confirm-button');

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

if (password && show_password_button) {
  show_password_button.addEventListener('click', function(event) {
    event.preventDefault();
    if (password.type === 'password') {
      password.type = 'text';
      show_password_button.textContent = '非表示';
    } else {
      password.type = 'password';
      show_password_button.textContent = '表示';
    }
  });
}

if (passwordConfirm && show_password_confirm_button) {
  show_password_confirm_button.addEventListener('click', function(event) {
    event.preventDefault();
    if (passwordConfirm.type === 'password') {
      passwordConfirm.type = 'text';
      show_password_confirm_button.textContent = '非表示';
    } else {
      passwordConfirm.type = 'password';
      show_password_confirm_button.textContent = '表示';
    }
  });
}
