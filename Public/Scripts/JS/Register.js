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
            User: {
                mailAddress: "",
                username: "",
            },
            System: {
                status: 0,
                message: "",
                url: "",
            },
        };
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
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
        this.setState((previous) => ({
            User: {
                ...previous.User,
                [name]: value,
            },
        }));
    }
    /**
     * Handling the form submission
     * @param {Event} event
     */
    handleSubmit(event) {
        const delay = 1975;
        event.preventDefault();
        fetch("/Users/New", {
            method: "POST",
            body: JSON.stringify({
                username: this.state.User.username,
                mailAddress: this.state.User.mailAddress,
            }),
            headers: {
                "Content-Type": "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        status: data.status,
                        message: data.message,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Handling the response from the server
     * @returns {string}
     */
    handleResponseColor() {
        if (this.state.System.status == 0) {
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
        if (this.state.System.status == 0) {
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
                <div>
                    <img src="/Public/Images/istockphoto-1175691444-612x612.jpg" />
                </div>
                <form method="POST" onSubmit={this.handleSubmit.bind(this)}>
                    <div id="label">Registration Form</div>
                    <input
                        type="text"
                        name="username"
                        placeholder="Username"
                        value={this.state.User.username}
                        onChange={this.handleChange.bind(this)}
                        required
                    />
                    <input
                        type="mail"
                        name="mailAddress"
                        placeholder="Mail Address"
                        value={this.state.User.mailAddress}
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
                            {this.state.System.message}
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
