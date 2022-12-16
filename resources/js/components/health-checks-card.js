export default () => ({
    sortedChecks: [],

    init() {
        this.$watch("$store.health.checks", (checks) => {
            this.sortedChecks = checks
                .sort((a, b) => b.result.order - a.result.order)
                .map(check => ({...check, timestamp: Date.now()}))
        })
    },
})
