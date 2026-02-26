// ActionButton.js
// Reusable ActionButton component for appointment actions and other usages

/**
 * Creates a styled action button element.
 * @param {Object} options - Button options
 * @param {string} options.iconClass - FontAwesome icon class (e.g., 'fa-eye')
 * @param {string} options.bgClass - Tailwind background color classes
 * @param {string} options.hoverClass - Tailwind hover color classes
 * @param {string} options.title - Tooltip/title for the button
 * @param {string} [options.extraClass] - Any extra classes
 * @param {string} [options.dataId] - Value for data-id attribute
 * @param {boolean} [options.disabled] - If true, disables the button
 * @param {string} [options.type] - Button type (default: 'button')
 * @returns {HTMLButtonElement}
 */
export function createActionButton({
    iconClass,
    bgClass,
    hoverClass,
    title,
    extraClass = '',
    dataId = '',
    disabled = false,
    type = 'button',
}) {
    const btn = document.createElement('button');
    btn.type = type;
    btn.className = `inline-flex items-center justify-center w-8 h-8 rounded-lg ${bgClass} ${hoverClass} text-white shadow-sm hover:shadow-md transition-all duration-200 text-sm ${extraClass}`;
    if (title) btn.title = title;
    if (dataId) btn.setAttribute('data-id', dataId);
    if (disabled) {
        btn.disabled = true;
        btn.style.opacity = '0.5';
        btn.style.cursor = 'not-allowed';
    }
    const icon = document.createElement('i');
    icon.className = `fas ${iconClass}`;
    btn.appendChild(icon);
    return btn;
}
