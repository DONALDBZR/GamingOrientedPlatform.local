/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            User: {
                username: "",
                mailAddress: "",
                profilePicture: "",
            },
            System: {
                status: 0,
                message: "",
                url: "",
                array: [],
            },
        };
        this.handleFileChange = this.handleFileChange.bind(this);
    }
    /**
     * Retrieving the session's data that is stored as a JSON to be used in the rendering
     * @returns {void}
     */
    getUser() {
        fetch("/Users/CurrentUser", {
            method: "GET",
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    User: {
                        username: data.User.username,
                        mailAddress: data.User.mailAddress,
                        profilePicture: data.User.profilePicture,
                    },
                })
            );
    }
    /**
     * Handling any change that is made in the interface
     * @param {Event} event
     * @returns {void}
     */
    handleFileChange(event) {
        this.setState({
            User: {
                profilePicture: event.target.files,
            },
            System: {
                array: [],
            },
        });
    }
    /**
     * Handling the form submission
     * @param {Event} event
     * @returns {void}
     */
    handleSubmit(event) {
        const delay = 2600;
        const formData = new FormData();
        for (
            let index = 0;
            index < this.state.User.profilePicture.length;
            index++
        ) {
            formData.append("image", this.state.User.profilePicture[index]);
        }
        event.preventDefault();
        fetch(`/Users/${this.state.User.username}/ProfilePicture`, {
            method: "POST",
            body: formData,
        })
            .then((response) => response.json())
            .then((data) =>
                this.setState({
                    System: {
                        success: data.success,
                        message: data.message,
                        url: data.url,
                    },
                })
            )
            .then(() => this.redirector(delay));
    }
    /**
     * Redirecting the user to an intended url
     * @param {number} delay
     * @returns {void}
     */
    redirector(delay) {
        setTimeout(() => {
            window.location.href = this.state.System.url;
        }, delay);
    }
    /**
     * Verifying the state before rendering the link
     * @returns {object}
     */
    verifyState() {
        if (this.state.User.profilePicture != null) {
            return (
                <a href={`/Users/Profile/${this.state.User.username}`}>
                    <img src={this.state.User.profilePicture} />
                </a>
            );
        } else {
            return (
                <a
                    href={`/Users/Profile/${this.state.User.username}`}
                    class="fa fa-user"
                ></a>
            );
        }
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <header>
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.User.username}`}>
                            Parkinston
                        </a>
                    </div>
                    <div>{this.verifyState()}</div>
                    <div>
                        <a href="/Sign-Out" class="fa fa-sign-out"></a>
                    </div>
                </nav>
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
    componentDidMount() {
        this.getUser();
    }
    render() {
        return (
            <main>
                <form
                    method="POST"
                    enctype="multipart/form-data"
                    onSubmit={this.handleSubmit.bind(this)}
                >
                    <div id="label">You can customize your profile picture</div>
                    <input
                        type="file"
                        name="image"
                        accept="image/*"
                        files={this.state.User.profilePicture}
                        onChange={this.handleFileChange.bind(this)}
                        required
                    />
                    <div>
                        <button>Change Profile Picture</button>
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
        return <footer>Parkinston</footer>;
    }
}
// Rendering the page
ReactDOM.render(<Application />, document.body);
