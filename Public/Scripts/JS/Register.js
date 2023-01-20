/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        /**
         * States of the application
         */
        this.state = {
            /**
             * Username of the user
             * @type {string}
             */
            username: "",
            /**
             * Mail address of the user
             * @type {string}
             */
            mailAddress: "",
            /**
             * The status returned from the request
             * @type {int}
             */
            status: 0,
            /**
             * The message that will be displayed to the user
             * @type {string}
             */
            message: "",
            /**
             * The url to be redirected after displaying the message
             * @type {string}
             */
            url: "",
        };
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
     * Handling any change that is made in the user interface
     * @param {Event} event
     */
    handleChange(event) {
        const target = event.target;
        const value = target.value;
        const name = target.name;
        this.setState({
            [name]: value,
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1975;
        event.preventDefault();
        fetch("/Controllers/Register.php", {
            method: "POST",
            body: JSON.stringify({
                username: this.state.username,
                mailAddress: this.state.mailAddress,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.body)
            .then((data) => console.log(data));
        // .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.status == 0) {
            return "rgb(0%, 100%, 0%)";
        } else {
            return "rgb(100%, 0%, 0%)";
        }
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseFontSize() {
        if (this.state.status == 0) {
            return "71%";
        } else {
            return "180%";
        }
    }
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
}
/**
 * The component that is the header
 */
class Header extends Application {
    render() {
        return (
            <header>
                <a href="/">Parkinston</a>
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
    render() {
        return (
            <main>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Registration Form</div>
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        value={this.state.username}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <input
                        type="mail"
                        name="mailAddress"
                        placeholder="Mail Address"
                        value={this.state.mailAddress}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <div id="button">
                        <button>Register</button>
                    </div>
                    <div id="response">
                        <h1
                            style={{
                                color: this.handleResponseColor(),
                                fontSize: this.handleResponseFontSize(),
                            }}
                        >
                            {this.state.message}
                        </h1>
                    </div>
                </form>
            </main>
        );
    }
}
/**
 * The component that is the footer
 */
class Footer extends Application {
    render() {
        return (
            <footer>
                <div>
                    Already have an account? Click <a href="/Login">Here</a> to
                    log in!
                </div>
                <div>Parkinston</div>
            </footer>
        );
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
