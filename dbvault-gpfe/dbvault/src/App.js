import React from 'react';
import { BrowserRouter as Router, Route, Switch } from 'react-router-dom';
import AlreadyUser from './components/AlreadyUser';
import NewUser from './components/NewUser';
import CreateAccount from './components/CreateAccount';

function App() {
  return (
    <Router>
      <div className="App">
        <Switch>
          <Route exact path="/" component={AlreadyUser} />
          <Route path="/new-user" component={NewUser} />
          <Route path="/create-account" component={CreateAccount} />
        </Switch>
      </div>
    </Router>
  );
}

export default App;
