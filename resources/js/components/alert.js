export default () => ({
    content() {
        return this.$store.alert.content
    },

    close() {
        this.$store.alert.content = null
    },

    bind: {
        "x-show"() {
            return this.content()
        },
        ":class"() {
            switch(this.$store.alert.color) {
                case "red": return "bg-red-light border-red-dark"
                case "green": return "bg-green-light border-green-dark"
                case "blue":
                default:
                    return "bg-blue-light border-blue-dark"
            }
        },
    },
})
