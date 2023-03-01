
import Alpine from "alpinejs"
import alert from "./components/alert.js"
import failedJobCard from "./components/failed-job-card.js"
import failedJobsCard from "./components/failed-jobs-card.js"
import healthChecksCard from "./components/health-checks-card.js"
import navbar from "./components/navbar.js"
import tokenFormCard from "./components/token-form-card.js"
import tokensCard from "./components/tokens-card.js"

window.Alpine = Alpine

Alpine.store("health", {
    about: {},
    checks: [],

    init() {
        this.getData()

        setInterval(() => this.getData(), 20000)
    },

    async getData() {
        const response = await getJson("/health")
        const data = await response.json()

        this.about = data.about || {}
        this.checks = data.checks || []
    },
})

Alpine.store("alert", {
    content: null,
    color: null,

    success(content) {
        this.content = content
        this.color = "green"
    },

    error(content) {
        this.content = content || "Something went wrong."
        this.color = "red"
    },
})

Alpine.data("alert", alert)
Alpine.data("failedJobCard", failedJobCard)
Alpine.data("failedJobsCard", failedJobsCard)
Alpine.data("healthChecksCard", healthChecksCard)
Alpine.data("navbar", navbar)
Alpine.data("tokenFormCard", tokenFormCard)
Alpine.data("tokensCard", tokensCard)

Alpine.start()
