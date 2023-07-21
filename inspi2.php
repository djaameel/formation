{% extends 'base.html.twig' %}

{% block title %}Categories{% endblock %}

{% block body %}
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Slug</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        {% for category in categories %}
            <tr>
                <td>{{ category.name }}</td>
                <td>{{ category.slug }}</td>
                <td>
                    <a href="{{ path('edit_category', {'id': category.id}) }}">Edit</a>
                    <a href="{{ path('delete_category', {'id': category.id}) }}">Delete</a>
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
{% endblock %}
