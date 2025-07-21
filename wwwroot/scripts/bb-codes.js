document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('text');
    const buttons = document.querySelectorAll('.bb-code-btn');

    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const tag = this.dataset.tag;
            const startTag = `[${tag}]`;
            const endTag = `[/${tag}]`;

            // Получаем текущее выделение и позицию курсора
            const startPos = textarea.selectionStart;
            const endPos = textarea.selectionEnd;
            const selectedText = textarea.value.substring(startPos, endPos);

            // Вставляем BB-коды
            textarea.value =
                textarea.value.substring(0, startPos) +
                startTag + selectedText + endTag +
                textarea.value.substring(endPos);

            // Устанавливаем курсор после закрывающего тега
            const newCursorPos = startPos + startTag.length + selectedText.length + endTag.length;
            textarea.setSelectionRange(newCursorPos, newCursorPos);

            // Возвращаем фокус в текстовое поле
            textarea.focus();
        });
    });

    // Обработка сочетаний клавиш (опционально)
    textarea.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && !e.altKey) {
            let tag = null;

            if (e.key === 'b') tag = 'b';
            else if (e.key === 'i') tag = 'i';
            else if (e.key === 'u') tag = 'u';

            if (tag) {
                e.preventDefault();
                const btn = document.querySelector(`.bb-code-btn[data-tag="${tag}"]`);
                btn.click(); // Имитируем клик по соответствующей кнопке
            }
        }
    });
});