# 🥗 Isa Melo Nutri - Sistema de Agendamento

Sistema web completo para gestão de consultório de nutrição, desenvolvido para facilitar o agendamento de consultas e o controle da agenda da profissional.

## 🚀 Funcionalidades

### 👤 Para o Paciente
* **Cadastro e Login:** Acesso seguro com área exclusiva e senha criptografada.
* **Agendamento Online:** Seleção de datas com verificação automática de disponibilidade.
* **Histórico:** Visualização de todas as consultas agendadas, realizadas ou canceladas.
* **Pagamento:** Simulação de checkout e confirmação visual de pagamento.
* **Autonomia:** Possibilidade de cancelar agendamentos (com confirmação de segurança).

### 👩‍⚕️ Para a Nutricionista (Admin)
* **Dashboard:** Visão geral rápida do número de atendimentos.
* **Agenda Digital:** Lista completa dos pacientes do dia e horários.
* **Bloqueio de Datas:** Ferramenta para bloquear dias de folga ou feriados, impedindo novos agendamentos nessas datas.
* **Gestão:** Visualização de status de pagamento e histórico.

## 🛠️ Tecnologias Utilizadas

* **Frontend:** HTML5, CSS3, Bootstrap 5 (Responsivo).
* **Backend:** PHP 8 (Vanilla - sem frameworks).
* **Banco de Dados:** MySQL.
* **Arquitetura:** MVC (Model-View-Controller) simplificado.
* **Segurança:** Hash de senhas (bcrypt), Prepared Statements (PDO) contra SQL Injection.

## ⚙️ Como rodar o projeto

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/ArthurRangel3211/isa-melo-nutri-app.git](https://github.com/ArthurRangel3211/isa-melo-nutri-app.git)
    ```

2.  **Configure o Banco de Dados:**
    * Crie um banco de dados no MySQL chamado `nutri_app`.
    * Importe o arquivo `database.sql` (disponível na raiz do projeto).

3.  **Configure a Conexão:**
    * Abra o arquivo `config/db.php`.
    * Verifique se as credenciais (usuário/senha) correspondem ao seu servidor local (padrão XAMPP: root / sem senha).

4.  **Acesse:**
    * Abra o navegador em `http://localhost/nutri-app` (ou o nome da sua pasta).

## 📄 Licença

Desenvolvido por **Arthur Rangel** para fins de portfólio acadêmico em Engenharia de Software.