export default (tokensUrl, deleteUrl) => ({
    loading: false,
    tokens: [],
    selectedIds: [],

    init() {
        this.getTokens()

        window.addEventListener("token-created", () => this.getTokens())
        window.addEventListener("delete-confirmed", () => this.deleteSelectedTokens())
    },

    selectAllCheckbox: {
        "x-ref": "selectAllCheckbox",
        "@click"(event) {
            this.selectedIds = event.target.checked
                ? this.tokens.map(job => job.id)
                : []
        },
    },

    async getTokens() {
        this.loading = true

        const response = await getJson(tokensUrl)

        if (response.ok) {
            this.tokens = await response.json()
        } else {
            this.tokens = []
        }

        this.loading = false
    },

    deleteSelectedTokens() {
        postJson(deleteUrl, {ids: this.selectedIds, _method: "delete"})
            .then(response => {
                if (! response.ok) {
                    return Promise.reject()
                }

                this.getTokens()

                this.$store.alert.success(
                    this.selectedIds.length > 1 ? "Tokens deleted." : "Token deleted."
                )
            })
            .catch(() => this.$store.alert.error())
            .finally(() => {
                this.selectedIds = []
                this.$refs.selectAllCheckbox.checked = false
            })
    },
})
