// Скрывании ссылки на лого на Главной ---------------------------------------------------------------
if (location.pathname == "/") {
$(document).ready(function () {
$('.headerLogo a').slideToggle('fast');
});
}