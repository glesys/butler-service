export default (id, retryUrl, forgetUrl) => ({
    init() {
        window.addEventListener("retry-confirmed", () => this.postIds(retryUrl))
        window.addEventListener("forget-confirmed", () => this.postIds(forgetUrl))
    },

    postIds(url) {
        postJson(url, {ids: [id]})
            .then(response => {
                if (response.ok) {
                    return window.location = "/health"
                }

                return Promise.reject()
            })
            .catch(() => this.$store.alert.error())
    },
})
