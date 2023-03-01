window.badgeClassName = (state) => {
    switch(state) {
        case "critical": return "bg-red-light"
        case "warning": return "bg-yellow-dark"
        case "ok": return "bg-green-light"
        case "unknown":
        default:
            return "bg-gray-300"
    }
}

window.deslug = (string) => string.replace(/[\W_]+/g, " ")

window.getJson = async (url) => await fetch(url, {
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
    },
})

window.postJson = async (url, data = {}) => await fetch(url, {
    method: "POST",
    body: JSON.stringify(data),
    headers: {
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-Token": document.head.querySelector("meta[name=csrf-token]").content,
    },
})
