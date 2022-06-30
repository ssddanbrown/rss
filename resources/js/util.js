const formats = [
    {limit: 45, label: 'second', div: 60},
    {limit: 50, label: 'minute', div: 60},
    {limit: 22, label: 'hour', div: 24},
    {limit: 6, label: 'day', div: 7},
    {limit: 51, label: 'week', div: 52},
    {limit: 10000, label: 'year', div: 1},
];


/**
 * @param {Number} timestamp
 */
export function timestampToRelativeTime(timestamp) {
    let label = '';
    let count = Math.abs((Date.now() / 1000) - timestamp);

    for (const format of formats) {
        label = format.label;
        if (count < format.limit) {
            break;
        }
        count = count / format.div;
    }

    const int = Math.round(count);

    return `${int} ${label}${int === 1 ? '' : 's'}`;
}
