/**
 * The application's back-end
 */
class BackEnd {
    constructor() {
        /**
         * Importing Express.js
         */
        this.express = require("express");
        this.application = this.express();
        /**
         * Calling the router of Express.js
         */
        this.router = this.express.Router("caseSensitive");
        /**
         * Port for the application
         */
        this.port = 8080;
        this.init();
    }
    /**
     * Initializing the application
     */
    init() {
        this.application.listen(process.env.PORT || this.port, () =>
            this.listener()
        );
        this.application.use(this.express.static(__dirname + "/Public"));
        this.handleRequest();
    }
    /**
     * Ensuring the the server is running
     */
    listener() {
        console.log(`Application running on port ${this.port}`);
    }
    /**
     * Handling the request that is sent from the client
     */
    handleRequest() {
        this.application.get("/", (request, response) => {
            response.response.sendFile(__dirname + "/Views/Homepage.html");
            console.log(`Application: /\nMethod: GET`);
        });
    }
}
const app = new BackEnd();
