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
        /**
         * List of modules needed
         */
        this._modulesPath = [
            "/Modules/React/18.2.0/react.production.min.js",
            "/Modules/React/18.2.0/react-dom.production.min.js",
            "/Modules/Babel/7.20.4/babel.min.js",
            "/Modules/Font-Awesome/6.2.1/fontawesome.min.js",
        ];
        this.init();
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
    /**
     * Initializing the application
     */
    init() {
        this.setRequestURI(window.location.pathname);
        if (this.getRequestURI() == "/") {
            this.setBodyId("Homepage");
        } else {
            this.setBodyId(this.getRequestURI().replaceAll("/", ""));
        }
        document.body.id = this.getBodyId();
        this.mount();
    }
    /**
     * Mounting the modules
     */
    mount() {
        for (let index = 0; index < this._modulesPath.length; index++) {
            const script = document.createElement("script");
            script.src = this._modulesPath[index];
            document.head.appendChild(script);
        }
    }
}
const application = new Parkinston();
