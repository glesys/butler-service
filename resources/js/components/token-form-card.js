export default (url) => ({
    consumer: "",
    name: "",
    abilities: "",
    errors: {},
    loading: false,

    submit() {
        this.loading = true

        postJson(url, {
            consumer: this.consumer,
            name: this.name,
            abilities: this.abilities.split(",").filter(n => n),
        })
        .then(async response => {
            const data = await response.json()

            if (response.ok) {
                this.reset()
                this.$dispatch("token-created")
                this.$store.alert.success("Token created: " + data.plainTextToken)
                return
            }

            if (data.errors) {
                this.errors = data.errors
            } else {
                return Promise.reject()
            }
        })
        .catch(() => this.$store.alert.error())
        .finally(() => this.loading = false)
    },

    reset() {
        this.consumer = ""
        this.name = ""
        this.abilities = ""
        this.errors = {}
    }
})
