/**
 * The Application that is going to be rendered in the DOM
 */
class Application extends React.Component {
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
        return <header>Parkinston</header>;
    }
}
/**
 * The component that is the main
 */
class Main extends Application {
    render() {
        return (
            <main>
                <div id="notice">
                    In order to use the application, you have the permissions
                    required.
                </div>
                <div id="buttons">
                    <div>
                        <a href="/Login">Login</a>
                    </div>
                    <div>
                        <a href="/Register">Register</a>
                    </div>
                </div>
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
