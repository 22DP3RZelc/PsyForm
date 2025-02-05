import React, { useState } from "react";
import axios from "axios";
import "../styles.css";

const Register = () => {
    const [form, setForm] = useState({ name: "", email: "", password: "", password_confirmation: "" });
    const [message, setMessage] = useState("");

    const handleChange = (e) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e) => {
        e.preventDefault();

        if (form.password !== form.password_confirmation) {
            setMessage("Passwords do not match");
            return;
        }

        try {
            const res = await axios.post("http://localhost/register.php", {
                name: form.name,
                email: form.email,
                password: form.password,
            });

            setMessage(res.data.message);
        } catch (error) {
            setMessage("Registration failed. Try again.");
        }
    };

    return (
        <div className="form-container">
            <h2>Register</h2>
            {message && <p>{message}</p>}
            <form onSubmit={handleSubmit}>
                <input type="text" name="name" placeholder="Name" onChange={handleChange} required />
                <input type="email" name="email" placeholder="Email" onChange={handleChange} required />
                <input type="password" name="password" placeholder="Password" onChange={handleChange} required />
                <input type="password" name="password_confirmation" placeholder="Confirm Password" onChange={handleChange} required />
                <button type="submit">Register</button>
            </form>
            <p>Already have an account? <a href="/login">Login</a></p>
        </div>
    );
};

export default Register;
