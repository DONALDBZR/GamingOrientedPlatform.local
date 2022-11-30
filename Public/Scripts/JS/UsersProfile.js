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
             * Username of the user
             */
            username: "",
            /**
             * Mail Address of the user
             */
            mailAddress: "",
            /**
             * Domain of the application
             */
            domain: "",
        };
    }
    /**
     * Renders the components that are being returned
     */
    render() {
        return [<Header />, <Main />, <Footer />];
    }
    /**
     * Retireving the session's data that is stored as a JSON to be used in the rendering
     */
    retrieveData() {
        fetch("/Users/CurrentUser",
            {
                method: "GET"
            })
            .then((response) => response.json())
            .then((data) => this.setState({
                username: data.username,
                mailAddress: data.mailAddress,
                domain: data.domain,
            }));
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
                <nav>
                    <div>
                        <a href={`/Users/Home/${this.state.username}`}>Parkinston</a>
                    </div>
                    <div>
                        <a href={`/Users/Profile/${this.state.username}`} class="fa fa-user"></a>
                    </div>
                    <div>
                        <a href="/Sign-Out" class="fa fa-sign-out"></a>
                    </div>
                </nav>
            </header>
        );
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
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
                <header>
                    <div id="profilePicture">
                        <i class="fa fa-user"></i>
                    </div>
                    <div id="username">{this.state.username}</div>
                </header>
                <description>
                    <div id="mailAddress">
                        <label>Mail Address:</label>
                        <div>{this.state.mailAddress}</div>
                    </div>
                    <div id="lolUsername">
                        <label>League of Legends's Username:</label>
                        <div>Darkness4869</div>
                    </div>
                </description>
            </main>
        );
    }
    /**
     * Methods to be run as soon as the component is mounted
     */
    componentDidMount() {
        this.retrieveData();
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
