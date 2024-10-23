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
const password_wraps = document.querySelectorAll('.password-wrap');

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

if (password_wraps) {
  password_wraps.forEach(password_wrap => {
    const password_input = password_wrap.querySelector('input');
    const eye_icons_wrap = password_wrap.querySelector('.eye-icons-wrap');
    const eye_icons = password_wrap.querySelectorAll('.icon');

    if (password_input && eye_icons_wrap && eye_icons) {
      eye_icons_wrap.addEventListener('click', function(event) {
        let input_type = password_input.getAttribute('type') === 'password' ? 'text' : 'password';
        password_input.setAttribute('type', input_type);

        eye_icons.forEach(eye_icon => {
          eye_icon.classList.toggle('show');
        });
      });
    }
  });
}
