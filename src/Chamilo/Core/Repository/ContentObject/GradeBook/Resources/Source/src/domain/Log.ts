export function logResponse(data: any) {
    const responseEl = document.getElementById('server-response');
    if (!responseEl) { return; }
    responseEl.innerHTML = typeof data === 'object' ? JSON.stringify(data, null, 4) : `<div>An error occurred:</div>${data}`;
}