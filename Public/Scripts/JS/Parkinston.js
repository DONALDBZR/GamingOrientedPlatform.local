/**
 * The main script that will initialize the application as needed
 */
class Parkinston {
    // Contructor method
    constructor() {
        /**
         * The request URI of the page needed
         * @type {string}
         */
        this.__requestUniformRequestInformation;
        /**
         * The ID of the body
         */
        this.__bodyId;
    }
    /**
     * Request URI accessor method
     * @returns {string}
     */
    getRequestURI() {
        return this.__requestUniformRequestInformation;
    }
    /**
     * Request URI mutator method
     * @param {string} request_uri
     */
    setRequestURI(request_uri) {
        this.__requestUniformRequestInformation = request_uri;
    }
    /**
     * Body ID accessor method
     * @returns {string}
     */
    getBodyId() {
        return this.__bodyId;
    }
    /**
     * Body ID mutator method
     * @param {string} body_id
     */
    setBodyId(body_id) {
        this.__bodyId = body_id;
    }
}
