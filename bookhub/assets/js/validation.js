/**
 * Простая клиентская проверка формы добавления и редактирования книги.
 */
document.addEventListener('DOMContentLoaded', function () {
    const bookForm = document.querySelector('[data-book-form]');

    if (!bookForm) {
        return;
    }

    bookForm.addEventListener('submit', function (event) {
        const title = bookForm.querySelector('[name="title"]').value.trim();
        const author = bookForm.querySelector('[name="author"]').value.trim();
        const year = Number(bookForm.querySelector('[name="year"]').value);
        const description = bookForm.querySelector('[name="description"]').value.trim();

        if (title.length < 2) {
            event.preventDefault();
            alert('Название книги должно содержать минимум 2 символа.');
            return;
        }

        if (author.length < 2) {
            event.preventDefault();
            alert('Имя автора должно содержать минимум 2 символа.');
            return;
        }

        if (year < 1800 || year > new Date().getFullYear()) {
            event.preventDefault();
            alert('Введите корректный год издания.');
            return;
        }

        if (description.length < 10) {
            event.preventDefault();
            alert('Описание должно содержать минимум 10 символов.');
        }
    });
});
