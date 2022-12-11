/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the properties of the component
         */
        this.state = {
            /**
             * Status of the response
             * @type {int}
             */
            status: "",
            /**
             * Message that is returned from the server
             * @type {string}
             */
            message: "",
            /**
             * URL for the redirector
             * @type {string}
             */
            url: "",
        };
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        /**
         * The amount of milliseconds that the registration process takes
         */
        const delay = 800;
        fetch("/Controllers/SignOut.php",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                status: data.status,
                message: data.message,
                url: data.url,
            }))
            .then(() => this.redirector(delay));
    }
    /**
     * Redirecting the user to an intended url
     * @param {int} delay 
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.url;
        }, delay);
    }
    /**
     * Renders the components that are being returned
     * @returns {Application} Component
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    constructor(props) {
        super(props);
    }
    render() {
        return (
            <header>
                Parkinston
            </header>
        );
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    constructor(props) {
        super(props);
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
    }
    render() {
        return (
            <main>
                {this.state.message}
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
