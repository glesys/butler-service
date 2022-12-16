export default () => ({
    state: "ok",

    init() {
        this.$watch("$store.health.checks", (checks) => {
            if (checks.some(check => check.result.state === "critical")) {
                this.state = "critical"
            } else if (checks.some(check => check.result.state === "warning")) {
                this.state = "warning"
            } else {
                this.state = "ok"
            }
        })
    },

    healthLink: {
        ":class"() {
            return {
                'text-red-dark': this.state === 'critical',
                'text-yellow-dark': this.state === 'warning',
                'animate-pulse': this.state !== 'ok',
            }
        },
    }
})
