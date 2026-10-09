/**
 * Arquivo de comportamentos JS da tela de consulta de pessoas
 * @author Ruan Pereira -> ruanpdev@outlook.com
 * @since 07/10/2026
 */
document.addEventListener("DOMContentLoaded", () => {
    const oInputPesquisa = document.querySelector("#inputPesquisa"),
          oBtnPesquisar = document.querySelector("#btnPesquisar"),
          oModal = document.querySelector("#modalCadastro"),
          oBtnIncluir = document.querySelector("#btnIncluir"),
          oBtnFecharModal = document.querySelector(".btn-fechar"),
          oFormCadastro = document.querySelector("#formCadastro");

    /**
     * Carrega a consulta de pessoas ao clicar no botão "Buscar"
     */
    oBtnPesquisar.addEventListener("click", () => {
        carregarPessoas(oInputPesquisa.value.trim());
    });

    /**
     * Carrega a consulta de pessoas ao apertar ENTER no campo "Pesquisar por nome..."
     */
    oInputPesquisa.addEventListener("keypress", (e) => {
        if (e.key === "Enter") {
            carregarPessoas(oInputPesquisa.value.trim());
        }
    });

    /**
     * Abre o modal de cadastro ao clicar no botão "Incluir"
     */
    oBtnIncluir.addEventListener("click", () => {
        oFormCadastro.reset();
        document.querySelector("#pessoaId").value = "";
        document.querySelector("#nome").disabled = false;
        document.querySelector("#cpf").disabled = false;
        document.querySelector("#btnSalvar").style.display = "block";
        document.querySelector("#secaoContatos").style.display = "none";
        document.querySelector("#tituloModal").innerText = "Cadastrar Nova Pessoa";
        oModal.style.display = "flex";

    });

    /**
     * Botão "X" para fechar o modal
     */
    oBtnFecharModal.addEventListener("click", () => {
        oModal.style.display = "none";
    });

    /**
     * Fecha o modal se clicar fora dele
     */
    window.addEventListener("click", (e) => {
        if (e.target === oModal) {
            oModal.style.display = "none";
        }
    });

    /**
     * Envia os dados do formulário via requisição para cadastrar ou alterar um pessoa
     */
    oFormCadastro.addEventListener("submit", async (e) => {
        e.preventDefault();
        let iId = document.querySelector("#pessoaId").value,
            sNome = document.querySelector("#nome").value.trim(),
            sCpf = document.querySelector("#cpf").value.trim(),
            sRota = iId ? '/pessoas/alterar' : '/pessoas/cadastrar',
            oDados = iId ? { id: iId, nome: sNome, cpf: sCpf } : { nome: sNome, cpf: sCpf },
            oResposta,
            oResultado;

        try {
            oResposta = await fetch(sRota, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(oDados)
            });

            oResultado = await oResposta.json();
            if (oResposta.ok && oResultado.sucesso) {
                alert(oResultado.mensagem);
                oModal.style.display = "none";
                carregarPessoas();
            } else {
                alert(oResultado.oErro || "Erro ao cadastrar pessoa.");
            }

        } catch (oErro) {
            console.error("Erro na requisição:", oErro);
            alert("Não foi possível conectar ao servidor.");
        }
    });

    /**
     * Trata o campo "CPF" do modal de manutenção em tempo real para adicionar a pontuação do CPF
     */
    document.getElementById('cpf').addEventListener('input', function (e) {
        let sValue = e.target.value.replace(/\D/g, '');

        if (sValue.length > 11) {
            sValue = sValue.slice(0, 11);
        }
        sValue = sValue.replace(/(\d{3})(\d)/, '$1.$2');
        sValue = sValue.replace(/(\d{3})(\d)/, '$1.$2');
        sValue = sValue.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        e.target.value = sValue;
    });

    /**
     * Envia os dados do formulário via requisição para cadastrar um contato
     */
    document.querySelector("#formAdicionarContato").addEventListener("submit", async (e) => {
        e.preventDefault();
        let idPessoa = document.querySelector("#pessoaId").value,
            tipoValue = document.querySelector("#contatoTipo").value,
            descricaoValue = document.querySelector("#contatoDescricao").value.trim(),
            oResposta,
            oResultado;

        try {
            oResposta = await fetch('/contatos/cadastrar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ pessoa_id: idPessoa, tipo: tipoValue, descricao: descricaoValue })
            });
            oResultado = await oResposta.json();

            if (oResposta.ok && oResultado.sucesso) {
                document.querySelector("#contatoDescricao").value = ""
                carregarContatosDaPessoa(idPessoa, true);
            } else {
                alert(oResultado.oErro || "Erro ao adicionar contato.");
            }
        } catch (oErro) {
            alert("Erro de conexão.");
        }
    });

    carregarPessoas();
});

/**
 * Busca os dados das pessoas via requisição e preenche a consulta
 * @param {string} sNomeFiltro 
 */
async function carregarPessoas(sNomeFiltro = "") {
    try {
        let oTabelaPessoasBody = document.querySelector("#tabelaPessoas tbody"),
            sUrl = sNomeFiltro ? `/pessoas?nome=${encodeURIComponent(sNomeFiltro)}` : '/pessoas',
            oResposta,
            aPessoas;

        oResposta = await fetch(sUrl, {
            headers: { 'Accept': 'application/json' }
        });
        if (!oResposta.ok) throw new Error("Erro ao buscar dados do servidor");
        aPessoas = await oResposta.json();

        oTabelaPessoasBody.innerHTML = "";
        if (aPessoas.length === 0) {
            oTabelaPessoasBody.innerHTML = `<tr><td colspan="4" style="text-align:center;">Nenhuma pessoa encontrada.</td></tr>`;
            return;
        }

        aPessoas.forEach(oPessoa => {
            let tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${oPessoa.id}</td>
                <td>${oPessoa.nome}</td>
                <td>${oPessoa.cpf}</td>
                <td>
                    <button class="btn-visualizar" onclick="visualizarPessoa(${oPessoa.id}, 'alterar')">Alterar</button>
                    <button class="btn-excluir" onclick="excluirPessoa(${oPessoa.id}, '${oPessoa.nome}')">Excluir</button>
                    <button class="btn-visualizar" onclick="visualizarPessoa(${oPessoa.id}, 'visualizar')">Visualizar</button>
                </td>
            `;
            oTabelaPessoasBody.appendChild(tr);
        });

    } catch (oErro) {
        console.error("Erro:", oErro);
        oTabelaPessoasBody.innerHTML = `<tr><td colspan="4" style="color:red; text-align:center;">Erro ao carregar os dados.</td></tr>`;
    }
}

/**
 * Executa a exclusão da pessoa
 * @param {int} iId
 * @param {string} sNome
 */
async function excluirPessoa(iId, sNome) {
    if (confirm(`Tem certeza que deseja excluir ${sNome}?`)) {
            let oResposta,
                oResultado;

        try {
            oResposta = await fetch(`/pessoas/excluir/${iId}`, {
                method: 'DELETE',
                headers: { 'Accept': 'application/json' }
            });
            oResultado = await oResposta.json();

            if (oResposta.ok && oResultado.sucesso) {
                alert(oResultado.mensagem);
                document.querySelector("#btnPesquisar").click();
            } else {
                alert(oResultado.oErro || "Erro ao excluir registro.");
            }
        } catch (oErro) {
            console.error("Erro na requisição de exclusão:", oErro);
            alert("Não foi possível conectar ao servidor para efetuar a exclusão.");
        }
    }
}

/**
 * Carrega os dados de uma pessoa e preenche o formulário com os campos disabled
 * @param {int} iId 
 * @param {string} sModo 
 */
async function visualizarPessoa(iId, sModo) {
    let oResposta,
        oPessoa;
    try {
        oResposta = await fetch(`/pessoas/buscar/${iId}`, {
            headers: { 'Accept': 'application/json' }
        });
        
        if (!oResposta.ok) throw new Error("Erro ao buscar dados");
        oPessoa = await oResposta.json();
        document.querySelector("#pessoaId").value = oPessoa.id;
        document.querySelector("#nome").value = oPessoa.nome;
        document.querySelector("#cpf").value = oPessoa.cpf;

        const oInputNome = document.querySelector("#nome"),
              oInputCpf = document.querySelector("#cpf"),
              oBtnSalvar = document.querySelector("#btnSalvar"),
              oFormContato = document.querySelector("#formAdicionarContato"),
              oSecaoContatos = document.querySelector("#secaoContatos");


        oSecaoContatos.style.display = "block";
        if (sModo === 'visualizar') {
            document.querySelector("#tituloModal").innerText = "Visualizar Pessoa";
            oInputNome.disabled = true;
            oInputCpf.disabled = true;
            oBtnSalvar.style.display = "none";
            oFormContato.style.display = "none";
            carregarContatosDaPessoa(oPessoa.id, false); 
        } else {
            document.querySelector("#tituloModal").innerText = "Alterar Pessoa";
            oInputNome.disabled = false;
            oInputCpf.disabled = false;
            oBtnSalvar.style.display = "block";
            oFormContato.style.display = "flex";
            carregarContatosDaPessoa(oPessoa.id, true);
        }
        document.querySelector("#modalCadastro").style.display = "flex";
    } catch (oErro) {
        console.error(oErro);
        alert("Erro ao carregar dados do registro.");
    }
}

/**
 * Carrega todos os contatos da pessoa informada
 * @param {int} iPessoaId 
 * @param {boolean} bPermitirAcoes 
 * @returns 
 */
async function carregarContatosDaPessoa(iPessoaId, bPermitirAcoes) {
    const oTabelaContatosBody = document.querySelector("#tabelaContatos tbody");
          oTabelaContatosBody.innerHTML = "<tr><td colspan='3'>Carregando contatos...</td></tr>";
    let oResposta,
        aContatos;

    try {
        oResposta = await fetch(`/contatos/pessoa/${iPessoaId}`, {
            headers: { 'Accept': 'application/json' }
        });
        aContatos = await oResposta.json();
        oTabelaContatosBody.innerHTML = "";

        if (aContatos.length === 0) {
            oTabelaContatosBody.innerHTML = "<tr><td colspan='3' style='text-align:center;'>Nenhum contato cadastrado.</td></tr>";
            return;
        }

        aContatos.forEach(oContato => {
            let tr = document.createElement("tr"),
                botaoExcluir = bPermitirAcoes 
                ? `<button class="btn-excluir" style="padding: 4px 8px; font-size:12px;" onclick="excluirContato(${oContato.id}, ${iPessoaId})">Excluir</button>`
                : `<span style="color:#aaa;">Somente leitura</span>`;

            tr.innerHTML = `
                <td>${((oContato.tipo == 1) ? 'Telefone' : 'Email')}</td>
                <td>${oContato.descricao}</td>
                <td>${botaoExcluir}</td>
            `;
            oTabelaContatosBody.appendChild(tr);
        });
    } catch (oErro) {
        oTabelaContatosBody.innerHTML = "<tr><td colspan='3' style='color:red;'>Erro ao carregar contatos.</td></tr>";
    }
}

/**
 * Executa a exclusão do contato selecionado
 * @param {int} iContatoId 
 * @param {int} iPessoaId 
 */
async function excluirContato(iContatoId, iPessoaId) {
    if (confirm("Remover este contato?")) {
        let oResposta,
            oResultado;

        try {
            oResposta = await fetch(`/contatos/excluir/${iContatoId}`, {
                method: 'DELETE'
            });
            oResultado = await oResposta.json();
            if (oResposta.ok && oResultado.sucesso) {
                carregarContatosDaPessoa(iPessoaId, true);
            } else {
                alert(oResultado.oErro || "Erro ao deletar.");
            }
        } catch (oErro) {
            alert("Erro de conexão.");
        }
    }
}