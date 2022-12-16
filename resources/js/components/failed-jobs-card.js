export default (failedJobsUrl, retryUrl, forgetUrl) => ({
    error: null,
    failedJobs: [],
    selectedIds: [],

    init() {
        this.getFailedJobs()

        setInterval(() => this.getFailedJobs(), 20000)

        window.addEventListener("retry-confirmed", () => this.retrySelectedJobs())
        window.addEventListener("forget-confirmed", () => this.forgetSelectedJobs())
    },

    selectAllCheckbox: {
        "x-ref": "selectAllCheckbox",
        "@click"(event) {
            this.selectedIds = event.target.checked
                ? this.failedJobs.map(job => job.id)
                : []
        },
    },

    async getFailedJobs() {
        const response = await getJson(failedJobsUrl)

        if (response.ok) {
            this.failedJobs = await response.json()

            this.error = null
        } else {
            this.error = "Could not fetch failed jobs."
            this.failedJobs = []
        }
    },

    retrySelectedJobs() {
        this.postIds(retryUrl, this.selectedIds.length > 1 ? "Jobs retried." : "Job retried.")
    },

    forgetSelectedJobs() {
        this.postIds(forgetUrl, this.selectedIds.length > 1 ? "Jobs forgotten." : "Job forgotten.")
    },

    postIds(url, successMessage) {
        postJson(url, {ids: this.selectedIds})
            .then(response => {
                if (! response.ok) {
                    return Promise.reject()
                }

                this.getFailedJobs()
                this.$store.health.getData()
                this.$store.alert.success(successMessage)
            })
            .catch(() => this.$store.alert.error())
            .finally(() => {
                this.selectedIds = []
                this.$refs.selectAllCheckbox.checked = false
            })
    }
})
